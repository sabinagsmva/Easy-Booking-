#include <iostream>
#include <fstream>
#include <sstream>
#include <string>
#include <regex>
#include <map>
#include <unordered_map>
#include <vector>
#include <algorithm>

struct AccessEntry {
    std::string ip;
    std::string timeRaw;
    std::string timeBucket;
    std::string method;
    std::string path;
    int status;
    std::string ua;
};

std::string bucketUA(const std::string &ua) {
    std::string s = ua;
    for (auto &c : s) c = std::tolower(c);
    if (s.find("chrome") != std::string::npos &&
        s.find("edg") == std::string::npos &&
        s.find("opr") == std::string::npos)
        return "Chrome";
    if (s.find("firefox") != std::string::npos)
        return "Firefox";
    if (s.find("safari") != std::string::npos &&
        s.find("chrome") == std::string::npos)
        return "Safari";
    if (s.find("edg") != std::string::npos)
        return "Edge";
    if (s.find("opera") != std::string::npos || s.find("opr") != std::string::npos)
        return "Opera";
    return "Other";
}

std::string toHourBucket(const std::string &timeRaw) {
    auto pos = timeRaw.find(':');
    if (pos == std::string::npos) return timeRaw;
    if (pos + 3 <= timeRaw.size())
        return timeRaw.substr(0, pos + 3);
    return timeRaw;
}

int main(int argc, char *argv[]) {
    if (argc < 2) {
        std::cerr << "Usage: " << argv[0] << " access_log_file\n";
        return 1;
    }

    std::string accessFile = argv[1];
    std::ifstream in(accessFile);
    if (!in) {
        std::cerr << "Cannot open " << accessFile << "\n";
        return 1;
    }

    std::vector<AccessEntry> entries;

   std::regex re(
    "^(\\S+) \\S+ \\S+ \\[([^\\]]+)\\] \\\"(\\S+)\\s+([^\\\"]*?)\\s+HTTP/[^\"]+\\\" (\\d{3}) (\\S+) \\\"([^\\\"]*)\\\" \\\"([^\\\"]*)\\\""
);

    std::string line;

    while (std::getline(in, line)) {
        std::smatch m;
        if (!std::regex_match(line, m, re)) continue;
        AccessEntry e;
        e.ip       = m[1];
        e.timeRaw  = m[2];
        e.method   = m[3];
        e.path     = m[4];
        e.status   = std::stoi(m[5]);
        e.ua       = m[8];

        auto qpos = e.path.find('?');
        if (qpos != std::string::npos)
            e.path = e.path.substr(0, qpos);

        e.timeBucket = toHourBucket(e.timeRaw);
        entries.push_back(e);
    }

    std::cout << "Parsed " << entries.size() << " access entries.\n";

    std::map<std::string,int> hitsByPage;
    std::map<std::string, std::map<std::string,int>> pageIpHits;
    std::map<std::string,int> browsers;
    std::map<std::string,int> timeline;
    std::map<std::string,int> timelineErrors;
    std::vector<AccessEntry> errorEntries;

    for (const auto &e : entries) {
        hitsByPage[e.path]++;
        pageIpHits[e.path][e.ip]++;
        browsers[bucketUA(e.ua)]++;
        timeline[e.timeBucket]++;

        if (e.status >= 400) {
            timelineErrors[e.timeBucket]++;
            errorEntries.push_back(e);
        }
    }

    {
        std::ofstream out("pages.csv");
        out << "page,hits\n";
        for (const auto &p : hitsByPage) {
            out << "\"" << p.first << "\"," << p.second << "\n";
        }
    }

    {
        std::ofstream out("pages_by_ip.csv");
        out << "page,ip,hits\n";
        for (const auto &p : pageIpHits) {
            for (const auto &ipcnt : p.second) {
                out << "\"" << p.first << "\","
                    << ipcnt.first << ","
                    << ipcnt.second << "\n";
            }
        }
    }

    {
        std::ofstream out("browsers.csv");
        out << "browser,hits\n";
        for (const auto &b : browsers) {
            out << b.first << "," << b.second << "\n";
        }
    }

    {
        std::ofstream out("timeline_hits.csv");
        out << "time_bucket,hits\n";
        for (const auto &t : timeline) {
            out << "\"" << t.first << "\"," << t.second << "\n";
        }
    }

    {
        std::ofstream out("timeline_errors.csv");
        out << "time_bucket,errors\n";
        for (const auto &t : timelineErrors) {
            out << "\"" << t.first << "\"," << t.second << "\n";
        }
    }

    {
        std::ofstream out("error_entries.csv");
        out << "time,ip,path,status,ua\n";
        for (const auto &e : errorEntries) {
            out << "\"" << e.timeRaw << "\","
                << e.ip << ","
                << "\"" << e.path << "\","
                << e.status << ","
                << "\"" << e.ua << "\"\n";
        }
    }

    return 0;
}
